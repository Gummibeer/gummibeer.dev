#!/usr/bin/env python3
"""Fast Archivist → Notion sync via REST APIs (no LLM, no MCP).

Requires:
  ARCHIVIST_API_TOKEN  (or ARCHIVIST_API_KEY)
  NOTION_API_TOKEN     (integration with "Update content" capability)

Optional env file: ~/.config/archivist-notion-sync/.env

Example:
  python3 sync_archivist_to_notion.py --notion-page https://notion.so/0123456789abcdef0123456789abcdef

  python3 sync_archivist_to_notion.py \\
    --session-id example_session_id \\
    --notion-page 0123456789abcdef0123456789abcdef

  python3 sync_archivist_to_notion.py --manifest batch.json
  python3 sync_archivist_to_notion.py --notion-page … --verify-only
"""
from __future__ import annotations

import argparse
import json
import os
import re
import subprocess
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path

SKILL_DIR = Path(__file__).resolve().parent.parent
BUILD_SECTIONS = SKILL_DIR / "scripts" / "build_sections.py"

ARCHIVIST_BASE = "https://api.myarchivist.ai"
NOTION_BASE = "https://api.notion.com"
NOTION_VERSION = "2025-09-03"
CAMPAIGN_ID = "YOUR_ARCHIVIST_CAMPAIGN_ID"
TRANSCRIPT_DS = "YOUR_NOTION_TRANSCRIPT_DATA_SOURCE_ID"
DEFAULT_ENV = Path.home() / ".config/archivist-notion-sync/.env"

EMPTY_ANCHORS = {
    "zusammenfassung": '### Zusammenfassung {toggle="true"}\n\t>',
    "handout": '### Handout {toggle="true"}\n\t```markdown\n\n\t```',
    "timeline": '### Timeline {toggle="true"}\n\t-',
}

SECTION_HEADINGS = {
    "zusammenfassung": "### Zusammenfassung",
    "handout": "### Handout",
    "timeline": "### Timeline",
}

ARCHIVIST_SESSION_RE = re.compile(r"/gamesessions/([a-z0-9]+)", re.I)


def load_env(path: Path | None) -> None:
    if path is None or not path.exists():
        return
    for line in path.read_text(encoding="utf-8").splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, _, val = line.partition("=")
        key, val = key.strip(), val.strip().strip('"').strip("'")
        os.environ.setdefault(key, val)


def api_key(*names: str) -> str:
    for name in names:
        val = os.environ.get(name, "").strip()
        if val:
            return val
    raise SystemExit(f"Missing API token; set one of: {', '.join(names)}")


def page_id_from(value: str) -> str:
    value = value.strip()
    m = re.search(r"([0-9a-f]{32})|([0-9a-f-]{36})", value, re.I)
    if not m:
        raise SystemExit(f"Not a Notion page id or URL: {value!r}")
    return (m.group(1) or m.group(2)).replace("-", "")


def archivist_url(session_id: str) -> str:
    return f"https://www.myarchivist.ai/campaigns/{CAMPAIGN_ID}/gamesessions/{session_id}"


def notion_page_url(page_id: str) -> str:
    return f"https://www.notion.so/{page_id_from(page_id)}"


def http_json(
    method: str,
    url: str,
    *,
    headers: dict[str, str],
    body: dict | None = None,
    timeout: int = 120,
    retries: int = 3,
) -> dict:
    data = None if body is None else json.dumps(body).encode("utf-8")
    last_err: Exception | None = None
    for attempt in range(1, retries + 1):
        req = urllib.request.Request(url, data=data, headers=headers, method=method)
        try:
            with urllib.request.urlopen(req, timeout=timeout) as resp:
                raw = resp.read().decode("utf-8")
                return json.loads(raw) if raw else {}
        except urllib.error.HTTPError as e:
            detail = e.read().decode("utf-8", errors="replace")[:800]
            raise SystemExit(f"HTTP {e.code} {method} {url}\n{detail}") from e
        except (TimeoutError, urllib.error.URLError) as e:
            last_err = e
            if attempt < retries:
                time.sleep(min(2 ** attempt, 8))
                continue
            raise SystemExit(f"Request failed after {retries} tries: {method} {url}\n{e}") from e
    raise SystemExit(f"Request failed: {method} {url}\n{last_err}")


def archivist_get(path: str, token: str) -> dict:
    url = f"{ARCHIVIST_BASE}{path}"
    headers = {"Authorization": f"Bearer {token}", "x-api-key": token, "Accept": "application/json"}
    return http_json("GET", url, headers=headers)


def notion_request(method: str, path: str, token: str, body: dict | None = None) -> dict:
    headers = {
        "Authorization": f"Bearer {token}",
        "Notion-Version": NOTION_VERSION,
        "Content-Type": "application/json",
        "Accept": "application/json",
    }
    return http_json(method, f"{NOTION_BASE}{path}", headers=headers, body=body)


def fetch_archivist(session_id: str, archivist_token: str) -> tuple[dict, dict]:
    q = urllib.parse.urlencode({"include_beats": "true", "include_moments": "true"})
    session = archivist_get(f"/v1/sessions/{session_id}?{q}", archivist_token)
    try:
        handout = archivist_get(f"/v1/sessions/{session_id}/handout", archivist_token)
    except SystemExit as e:
        if "HTTP 404" in str(e):
            handout = {"summary": session.get("summary") or ""}
        else:
            raise
    return session, handout


def run_build_sections(session: dict, handout: dict, out_dir: Path) -> dict:
    out_dir.mkdir(parents=True, exist_ok=True)
    session_path = out_dir.parent / "session.json"
    handout_path = out_dir.parent / "handout.json"
    session_path.write_text(json.dumps(session, ensure_ascii=False, indent=2), encoding="utf-8")
    handout_path.write_text(json.dumps(handout, ensure_ascii=False, indent=2), encoding="utf-8")
    subprocess.check_call(
        [
            sys.executable,
            str(BUILD_SECTIONS),
            "--session",
            str(session_path),
            "--handout",
            str(handout_path),
            "--out-dir",
            str(out_dir),
        ]
    )
    return json.loads((out_dir / "meta.json").read_text(encoding="utf-8"))


def get_page(page_id: str, notion_token: str) -> dict:
    return notion_request("GET", f"/v1/pages/{page_id_from(page_id)}", notion_token)


def session_id_from_url(url: str) -> str:
    m = ARCHIVIST_SESSION_RE.search(url)
    if not m:
        raise SystemExit(f"Cannot parse Archivist session id from URL: {url!r}")
    return m.group(1)


def resolve_session_id(
    notion_page: str,
    notion_token: str,
    explicit_session_id: str | None,
) -> str:
    if explicit_session_id:
        return explicit_session_id.strip()
    page = get_page(notion_page, notion_token)
    archivist_url = (page.get("properties") or {}).get("Archivist", {}).get("url")
    if not archivist_url:
        raise SystemExit(
            "No --session-id and Notion page has no Archivist URL property. "
            "Set Archivist on the page or pass --session-id."
        )
    return session_id_from_url(archivist_url)


def page_has_transcript(notion_page: str, notion_token: str) -> bool:
    page = get_page(notion_page, notion_token)
    relation = (page.get("properties") or {}).get("Transkripte", {}).get("relation") or []
    return bool(relation)


def extract_section_block(live: str, section: str) -> str | None:
    heading = SECTION_HEADINGS.get(section)
    if not heading or heading not in live:
        return None
    start = live.index(heading)
    next_start = len(live)
    for other in SECTION_HEADINGS.values():
        if other == heading:
            continue
        pos = live.find(other, start + len(heading))
        if pos >= 0:
            next_start = min(next_start, pos)
    notizen = live.find("### Notizen", start + len(heading))
    if notizen >= 0:
        next_start = min(next_start, notizen)
    return live[start:next_start].rstrip("\n")


def get_page_markdown(page_id: str, notion_token: str) -> str:
    data = notion_request("GET", f"/v1/pages/{page_id_from(page_id)}/markdown", notion_token)
    return data.get("markdown") or data.get("content") or ""


def verify_page(page_id: str, notion_token: str) -> dict:
    page = get_page(page_id, notion_token)
    props = page.get("properties") or {}
    title_parts = props.get("Name", {}).get("title") or []
    title = title_parts[0]["plain_text"] if title_parts else ""
    archivist = props.get("Archivist", {}).get("url")
    transcripts = props.get("Transkripte", {}).get("relation") or []
    icon = (page.get("icon") or {}).get("emoji")
    md = get_page_markdown(page_id, notion_token)
    sections = {}
    for key in ("zusammenfassung", "handout", "timeline"):
        block = extract_section_block(md, key) or ""
        filled = key == "zusammenfassung" and len(block) > 60 and "\t> " in block
        filled = filled or (key == "handout" and "```markdown" in block and len(block) > 80)
        filled = filled or (key == "timeline" and block.count("- **") >= 1)
        sections[key] = {"filled": filled, "chars": len(block)}
    ok = bool(title and archivist and icon == "⏳" and all(s["filled"] for s in sections.values()))
    return {
        "ok": ok,
        "title": title,
        "archivist_url": archivist,
        "icon": icon,
        "transcript_count": len(transcripts),
        "sections": sections,
        "notizen_preserved": "### Notizen" in md,
        "page_url": notion_page_url(page_id),
    }


def update_section_content(
    page_id: str,
    notion_token: str,
    section: str,
    new_str: str,
    live_md: str | None = None,
) -> None:
    anchor = EMPTY_ANCHORS.get(section)
    if anchor:
        try:
            update_markdown_section(page_id, notion_token, anchor, new_str)
            return
        except SystemExit:
            pass
    live = live_md if live_md is not None else get_page_markdown(page_id, notion_token)
    old = extract_section_block(live, section)
    if not old:
        raise SystemExit(f"Cannot find {section} section on Notion page for replace")
    update_markdown_section(page_id, notion_token, old, new_str)


def update_markdown_section(
    page_id: str,
    notion_token: str,
    old_str: str,
    new_str: str,
) -> None:
    notion_request(
        "PATCH",
        f"/v1/pages/{page_id}/markdown",
        notion_token,
        {
            "type": "update_content",
            "update_content": {
                "content_updates": [{"old_str": old_str, "new_str": new_str}],
            },
        },
    )


def create_transcript_page(
    title: str,
    session_id: str,
    session_page_id: str,
    notion_token: str,
) -> str:
    pid = page_id_from(session_page_id)
    body = {
        "parent": {"type": "data_source_id", "data_source_id": TRANSCRIPT_DS},
        "properties": {
            "Name": {"title": [{"type": "text", "text": {"content": f"{title} (MyArchivist)"}}]},
            "Quelle": {"select": {"name": "MyArchivist"}},
            "Link": {"url": archivist_url(session_id)},
            "Sitzung": {"relation": [{"id": pid}]},
        },
    }
    page = notion_request("POST", "/v1/pages", notion_token, body)
    transcript_id = page["id"]
    notion_request(
        "PATCH",
        f"/v1/pages/{transcript_id}/markdown",
        notion_token,
        {
            "type": "insert_content",
            "insert_content": {"content": "```markdown\n\n```", "position": {"type": "start"}},
        },
    )
    return transcript_id


def update_session_properties(
    page_id: str,
    title: str,
    session_id: str,
    notion_token: str,
    transcript_page_id: str | None = None,
) -> None:
    pid = page_id_from(page_id)
    properties: dict = {
        "Name": {"title": [{"type": "text", "text": {"content": title}}]},
        "Archivist": {"url": archivist_url(session_id)},
    }
    if transcript_page_id:
        properties["Transkripte"] = {"relation": [{"id": page_id_from(transcript_page_id)}]}
    notion_request(
        "PATCH",
        f"/v1/pages/{pid}",
        notion_token,
        {
            "icon": {"type": "emoji", "emoji": "⏳"},
            "properties": properties,
        },
    )


def sync_one(
    *,
    session_id: str | None,
    notion_page: str,
    archivist_token: str,
    notion_token: str,
    work_dir: Path | None,
    skip_transcript: bool | None,
    force_transcript: bool,
    dry_run: bool,
) -> dict:
    t0 = time.perf_counter()
    page_id = page_id_from(notion_page)
    resolved_session_id = resolve_session_id(notion_page, notion_token, session_id)
    should_skip_transcript = skip_transcript
    if should_skip_transcript is None:
        should_skip_transcript = page_has_transcript(notion_page, notion_token) and not force_transcript

    print(f"Sync {resolved_session_id} → {notion_page_url(page_id)}")
    if should_skip_transcript:
        print("  Transcript: skip (existing relation or --skip-transcript)")

    session, handout = fetch_archivist(resolved_session_id, archivist_token)
    beats = session.get("beats") or []
    print(f"  Archivist: {session.get('title')} ({len(beats)} beats) [{time.perf_counter() - t0:.1f}s]")

    out_root = work_dir or Path.home() / ".archivist-sync" / resolved_session_id
    out_dir = out_root / "out"
    meta = run_build_sections(session, handout, out_dir)
    title = meta["title"]
    print(f"  Built sections [{time.perf_counter() - t0:.1f}s]")

    if dry_run:
        print(f"  Dry run — would write {title!r} to {page_id}")
        return {"dry_run": True, "session_id": resolved_session_id, "title": title, "page_url": notion_page_url(page_id)}

    transcript_id = None
    if not should_skip_transcript:
        transcript_id = create_transcript_page(title, resolved_session_id, page_id, notion_token)
        print(f"  Transcript: {notion_page_url(transcript_id)} [{time.perf_counter() - t0:.1f}s]")

    update_session_properties(page_id, title, resolved_session_id, notion_token, transcript_id)
    print(f"  Properties + icon ⏳ [{time.perf_counter() - t0:.1f}s]")

    live_md = get_page_markdown(page_id, notion_token)
    for section in EMPTY_ANCHORS:
        new_str = (out_dir / f"{section}.md").read_text(encoding="utf-8")
        update_section_content(page_id, notion_token, section, new_str, live_md=live_md)
        live_md = get_page_markdown(page_id, notion_token)
        print(f"  Updated {section} [{time.perf_counter() - t0:.1f}s]")

    result = verify_page(page_id, notion_token)
    result["elapsed_s"] = round(time.perf_counter() - t0, 1)
    result["session_id"] = resolved_session_id
    print(
        f"  Done in {result['elapsed_s']}s — verify={'OK' if result['ok'] else 'CHECK MANUALLY'}"
    )
    return result


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("--env-file", type=Path, default=DEFAULT_ENV)
    parser.add_argument("--session-id", help="MyArchivist gamesession id (optional if Archivist URL on page)")
    parser.add_argument("--notion-page", help="Notion Sitzungen page id or URL")
    parser.add_argument("--manifest", type=Path, help="JSON array of {archivist_id?, page_id|page_url}")
    parser.add_argument("--work-dir", type=Path, help="Cache dir for session.json / out/")
    parser.add_argument("--skip-transcript", action="store_true", help="Never create a transcript page")
    parser.add_argument(
        "--force-transcript",
        action="store_true",
        help="Create transcript even when Transkripte relation already exists",
    )
    parser.add_argument("--verify-only", action="store_true", help="Sanity-check page; no writes")
    parser.add_argument("--json", action="store_true", help="Print verify result as JSON on stdout")
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()

    load_env(args.env_file)
    archivist_token = api_key("ARCHIVIST_API_TOKEN", "ARCHIVIST_API_KEY", "MYARCHIVIST_API_KEY")
    notion_token = api_key("NOTION_API_TOKEN", "NOTION_API_KEY")

    skip_transcript: bool | None = True if args.skip_transcript else None

    if args.verify_only:
        if not args.notion_page:
            parser.error("--verify-only requires --notion-page")
        result = verify_page(page_id_from(args.notion_page), notion_token)
        if args.json:
            print(json.dumps(result, ensure_ascii=False, indent=2))
        else:
            print(f"{result['title']} — {'OK' if result['ok'] else 'CHECK'}")
            print(f"  {result['page_url']}")
            for name, info in result["sections"].items():
                print(f"  {name}: {'filled' if info['filled'] else 'empty'} ({info['chars']} chars)")
        raise SystemExit(0 if result["ok"] else 1)

    if args.manifest:
        entries = json.loads(args.manifest.read_text(encoding="utf-8"))
        results = []
        for i, entry in enumerate(entries, 1):
            sid = entry.get("archivist_id") or entry.get("session_id")
            page = entry.get("page_id") or entry.get("page_url") or entry.get("notion_page")
            print(f"\n[{i}/{len(entries)}]")
            results.append(
                sync_one(
                    session_id=sid,
                    notion_page=page,
                    archivist_token=archivist_token,
                    notion_token=notion_token,
                    work_dir=args.work_dir,
                    skip_transcript=skip_transcript,
                    force_transcript=args.force_transcript,
                    dry_run=args.dry_run,
                )
            )
        if args.json:
            print(json.dumps(results, ensure_ascii=False, indent=2))
        return

    if not args.notion_page:
        parser.error("Provide --notion-page (and optional --session-id), --manifest, or --verify-only")

    result = sync_one(
        session_id=args.session_id,
        notion_page=args.notion_page,
        archivist_token=archivist_token,
        notion_token=notion_token,
        work_dir=args.work_dir,
        skip_transcript=skip_transcript,
        force_transcript=args.force_transcript,
        dry_run=args.dry_run,
    )
    if args.json:
        print(json.dumps(result, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
