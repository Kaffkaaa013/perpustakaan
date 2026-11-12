import json

path = r"C:\Users\maike\.gemini\antigravity-ide\brain\c41da1ee-e33c-42fa-8dd1-b9d4d111b30e\.system_generated\logs\transcript_full.jsonl"
with open(path, "r", encoding="utf-8") as f:
    for line in f:
        d = json.loads(line)
        if d.get("step_index") == 35:
            with open("step_35_content.txt", "w", encoding="utf-8") as out:
                out.write(d.get("content", ""))
            print("Wrote step_35_content.txt successfully, length:", len(d.get("content", "")))
            break
