import json
import os

transcript_path = r"C:\Users\CODDY\.gemini\antigravity-ide\brain\bcc0a0cb-130a-4b14-a370-19cffa82479c\.system_generated\logs\transcript.jsonl"
target_file = r"c:\xampp\htdocs\navi\resources\js\Pages\Admin\Despachos\Index.vue"
out_file = r"c:\xampp\htdocs\navi\Index_original.vue"

content_found = None

with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        data = json.loads(line)
        
        # We look for a view_file response that contains the full file
        # The output of view_file looks like: "File Path: ...\nTotal Lines: ...\nShowing lines ... to ...\n1: <script setup>\n..."
        if data.get('type') == 'TOOL_RESPONSE':
            for tc in data.get('tool_responses', []):
                out = tc.get('output', '')
                if 'File Path: `file:///c:/xampp/htdocs/navi/resources/js/Pages/Admin/Despachos/Index.vue`' in out.lower():
                    if 'Showing lines 1 to' in out:
                        # Extract the code
                        lines = out.split('\n')
                        code_lines = []
                        for l in lines:
                            if l and l[0].isdigit() and l.find(': ') != -1:
                                code_lines.append(l[l.find(': ')+2:])
                        if code_lines:
                            content_found = '\n'.join(code_lines)
                            break
            if content_found:
                break

if content_found:
    with open(out_file, 'w', encoding='utf-8') as f:
        f.write(content_found)
    print("Extracted original Index.vue successfully!")
else:
    print("Could not find original Index.vue in transcript.")
