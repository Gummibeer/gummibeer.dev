#!/usr/bin/env python3
"""Build Notion section markdown from MyArchivist session + handout JSON."""
from __future__ import annotations

import argparse
import json
from pathlib import Path


def blockquote_summary(text: str) -> str:
    """Single Notion blockquote; use <br> for line/paragraph breaks (not multiple > lines)."""
    paragraphs = [p.strip() for p in text.strip().split("\n\n") if p.strip()]
    parts = [p.replace("\n", "<br>") for p in paragraphs]
    return f"\t> {'<br><br>'.join(parts)}"


def build_handout_md(h: dict) -> str:
    parts = [
        "## Session Outline",
        h["sessionOutline"].strip(),
        "",
        "## Session Summary",
        h["summary"].strip(),
    ]

    if h.get("moments"):
        parts.extend(["", "## Key Moments"])
        for m in h["moments"]:
            parts.extend([f"### {m['label']}", m["content"].strip(), ""])

    if h.get("encounters"):
        parts.extend(["", "## Encounters"])
        for e in h["encounters"]:
            parts.append(f"### {e['title']}")
            for b in e.get("bullets", []):
                parts.append(f"- {b}")
            parts.append("")

    if h.get("characterSpotlight"):
        parts.extend(["", "## Character Highlights"])
        for c in h["characterSpotlight"]:
            parts.append(f"### {c['name']}")
            parts.append("")
            parts.append(c["description"].strip())
            parts.append("")
            for b in c.get("bullets", []):
                parts.append(f"- {b}")
            parts.append("")

    if h.get("otherEntitySpotlight"):
        parts.extend(["", "## Other Entity Spotlight"])
        for e in h["otherEntitySpotlight"]:
            parts.extend([f"### {e['name']}", e["description"].strip(), ""])

    if h.get("items"):
        parts.extend(["", "## Items"])
        for item in h["items"]:
            parts.extend([f"### {item['name']}", item["description"].strip(), ""])

    if h.get("valuableInformation"):
        parts.extend(["", "## Valuable Information"])
        for v in h["valuableInformation"]:
            parts.append(f"- {v['info']}")
        parts.append("")

    ps = h.get("partyStatusAndNextSteps") or {}
    if ps.get("partyStatus"):
        parts.extend(["", "## Party Status", ps["partyStatus"]["summary"].strip(), ""])
        for b in ps["partyStatus"].get("bullets", []):
            parts.append(f"- {b}")
        parts.append("")

    if ps.get("nextSteps"):
        parts.extend(["", "## Next Steps", ps["nextSteps"]["summary"].strip()])

    return "\n".join(parts).strip()


def build_timeline(session: dict) -> str:
    beats = session.get("beats") or []
    children: dict = {}
    for b in beats:
        children.setdefault(b.get("parent_id"), []).append(b)
    for pid in children:
        children[pid].sort(key=lambda x: x.get("index", 0))

    def render_beat(beat: dict, depth: int) -> list[str]:
        indent = "\t" * depth
        desc = (beat.get("description") or "").strip()
        label = beat.get("label") or ""
        line = f"{indent}- **{label}**"
        if desc:
            line += f"<br>{desc}"
        lines = [line]
        for child in children.get(beat["id"], []):
            lines.extend(render_beat(child, depth + 1))
        return lines

    lines: list[str] = []
    for major in children.get(None, []):
        lines.extend(render_beat(major, 1))
    return "\n".join(lines)


def build_sections(
    session: dict,
    handout: dict,
    *,
    notizen_extra: str | None = None,
) -> dict[str, str]:
    summary_md = blockquote_summary(session["summary"])
    handout_md = build_handout_md(handout)
    timeline_md = build_timeline(session)
    notizen = "### Notizen {toggle=\"true\"}"
    if notizen_extra:
        notizen += f"\n{notizen_extra}"

    return {
        "title": session.get("title") or "",
        "zusammenfassung": f"### Zusammenfassung {{toggle=\"true\"}}\n{summary_md}",
        "handout": f"### Handout {{toggle=\"true\"}}\n\t```markdown\n{handout_md}\n\t```",
        "timeline": f"### Timeline {{toggle=\"true\"}}\n{timeline_md}",
        "notizen": notizen,
    }


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--session", type=Path, required=True, help="session.json from MyArchivist")
    parser.add_argument("--handout", type=Path, required=True, help="handout.json from MyArchivist")
    parser.add_argument("--out-dir", type=Path, required=True, help="Output directory for section .md files")
    parser.add_argument("--notizen", type=str, default="", help="Optional extra markdown under Notizen")
    args = parser.parse_args()

    session = json.loads(args.session.read_text(encoding="utf-8"))
    handout = json.loads(args.handout.read_text(encoding="utf-8"))
    extra = args.notizen.strip() or None
    sections = build_sections(session, handout, notizen_extra=extra)

    args.out_dir.mkdir(parents=True, exist_ok=True)
    for key, value in sections.items():
        if key == "title":
            continue
        (args.out_dir / f"{key}.md").write_text(value, encoding="utf-8")
    meta = {"title": sections["title"]}
    (args.out_dir / "meta.json").write_text(json.dumps(meta, indent=2), encoding="utf-8")
    print(sections["title"], "→", args.out_dir)


if __name__ == "__main__":
    main()
