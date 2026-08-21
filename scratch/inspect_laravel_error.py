import subprocess
import re

out = subprocess.check_output(['curl.exe', '-s', 'https://fgd-ua.vercel.app'], text=True, encoding='utf-8', errors='ignore')

# Search for exception class, message, or title in Ignition / Whoops output
matches = re.findall(r'<h1[^>]*>(.*?)</h1>', out, re.DOTALL | re.IGNORECASE)
print("H1 Matches:", matches)

matches_title = re.findall(r'<title>(.*?)</title>', out, re.DOTALL | re.IGNORECASE)
print("Title Matches:", matches_title)

# Search for class names or exception text
class_matches = re.findall(r'class="[^"]*exception[^"]*"[^>]*>(.*?)</div>', out, re.DOTALL | re.IGNORECASE)
print("Class Matches:", class_matches[:5])

# Print lines containing "Exception" or "Error" or "SQL"
lines = [line.strip() for line in out.split('\n') if any(w in line for w in ['Exception', 'Error', 'PDO', 'SQLSTATE', 'Illuminate', 'Symfony', 'vendor'])]
print("Relevant error lines (first 20):")
for l in lines[:20]:
    print(l[:150])
