#!/usr/bin/env python3
"""Emit notion-update-page JSON for one section (stdout)."""
from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path


ANCHORS = {
    "zusammenfassung": '### Zusammenfassung {toggle="true"}\n\t>',
    "handout": '### Handout {toggle="true"}\n\t```markdown\n\n\t```',
    "timeline": '### Timeline {toggle="true"}\n\t-',
    "notizen": '### Notizen {toggle="true"}',
}


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--page-id", required=True)
    parser.add_argument("--section", choices=ANCHORS.keys(), required=True)
    parser.add_argument("--content-file", type=Path, required=True)
    args = parser.parse_args()

    new_str = args.content_file.read_text(encoding="utf-8")
    old_str = ANCHORS[args.section]
    if args.section == "timeline" and not new_str.strip().endswith("\n"):
        # Anchor includes first list item marker; section file is full heading + body
        if new_str.startswith("### Timeline"):
            old_str = '### Timeline {toggle="true"}\n\t-'
        else:
            old_str = '### Timeline {toggle="true"}\n'

    payload = {
        "page_id": args.page_id,
        "command": "update_content",
        "content_updates": [{"old_str": old_str, "new_str": new_str}],
    }
    json.dump(payload, sys.stdout, ensure_ascii=False)


if __name__ == "__main__":
    main()
