import subprocess
import re

out = subprocess.check_output(['curl.exe', '-s', 'https://fgd-ua.vercel.app'], text=True, encoding='utf-8', errors='ignore')

# Extract trace lines
frames = re.findall(r'<span data-tippy-content="([^"]+)">', out)
print("Frames:")
for i, f in enumerate(frames):
    print(f" {i}: {f}")

snippets = re.findall(r'<div x-show="!highlightedCode"><pre class="truncate"><code>(.*?)</code></pre></div>', out)
print("\nCode Snippets in Trace:")
for i, s in enumerate(snippets):
    print(f" {i}: {s}")
