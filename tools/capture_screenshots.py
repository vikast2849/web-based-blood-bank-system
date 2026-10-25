import subprocess
import os
import http.cookiejar
import urllib.request
import urllib.parse

CHROME = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
OUT_DIR = r"C:\Users\haris\.gemini\antigravity\scratch\web-based-blood-bank-system\docs\screenshots"
os.makedirs(OUT_DIR, exist_ok=True)

def snap(url, filename, width=1280, height=800):
    dest = os.path.join(OUT_DIR, filename)
    cmd = [
        CHROME,
        "--headless=new",
        f"--screenshot={dest}",
        f"--window-size={width},{height}",
        "--hide-scrollbars",
        url
    ]
    subprocess.run(cmd, check=True)
    print(f"Captured {filename} ({width}x{height})")

# 1. Public Pages (Desktop)
snap("http://localhost:8000/login.php", "login.png", 1280, 800)
snap("http://localhost:8000/register.php", "register.png", 1280, 800)
snap("http://localhost:8000/abs.php", "available_samples.png", 1280, 800)
snap("http://localhost:8000/info.php", "home.png", 1280, 800)

# 2. Public Pages (Mobile: 375x720)
snap("http://localhost:8000/login.php", "login_mobile.png", 375, 720)
snap("http://localhost:8000/register.php", "register_mobile.png", 375, 720)
snap("http://localhost:8000/abs.php", "available_samples_mobile.png", 375, 720)
snap("http://localhost:8000/info.php", "blood_guide_mobile.png", 375, 720)

print("Screenshots captured successfully.")
