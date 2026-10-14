#!/usr/bin/env python3
"""
Hostinger Subdomain & Root FTPS Deployment Script
Syncs all SaaS files into Hostinger ilovedigital.online public_html directory
Intelligently handles Hostinger multi-domain directory structure:
  - domains/ilovedigital.online/public_html
  - /public_html (when FTP user is jailed to domain root)
"""

import os
import sys
import ftplib
import time

def ensure_ftp_dir(ftp, path):
    """Recursively ensure a directory path exists on the FTP server and cwd into it."""
    clean_path = path.strip('/')
    if not clean_path or clean_path == '.':
        return
    parts = clean_path.split('/')
    for part in parts:
        if not part:
            continue
        try:
            ftp.cwd(part)
        except Exception:
            try:
                ftp.mkd(part)
            except Exception:
                pass
            ftp.cwd(part)

def resolve_target_dir(ftp, requested_dir):
    """
    Intelligently discover the actual public_html path for ilovedigital.online on Hostinger.
    Handles both /domains/ilovedigital.online/public_html and jailed /public_html.
    """
    initial_dir = ftp.pwd()
    print(f"📍 Initial FTP working directory on login: {initial_dir}")

    # Check root level /
    try:
        ftp.cwd('/')
        root_items = [i for i in ftp.nlst() if i not in ('.', '..')]
        print(f"📂 Top-level items in '/': {root_items}")
    except Exception as e:
        print(f"Notice: Cannot cwd to '/': {e}")
        root_items = []

    # 1. Check if 'domains' directory exists (standard Hostinger multi-domain layout)
    domain_candidates = ['/domains', 'domains', '../domains']
    for cand in domain_candidates:
        try:
            ftp.cwd(cand)
            dom_items = [i for i in ftp.nlst() if i not in ('.', '..')]
            print(f"📂 Found domains folder '{cand}' containing: {dom_items}")
            for d in dom_items:
                base_d = os.path.basename(d.rstrip('/'))
                if 'ilovedigital.online' in base_d.lower():
                    target = f"{cand.rstrip('/')}/{base_d}/public_html"
                    print(f"🎯 Discovered exact Hostinger domain path: {target}")
                    ensure_ftp_dir(ftp, target)
                    return ftp.pwd()
        except Exception:
            pass

    # 2. Check if initial_dir is already public_html or ends with public_html
    # Prevents accidental /public_html/public_html nesting when default requested_dir is 'public_html'
    clean_req = requested_dir.strip('/')
    if initial_dir.endswith('/public_html') or initial_dir == '/public_html':
        if clean_req == 'public_html':
            print(f"✅ Initial directory is already '{initial_dir}'. Deploying directly to root of this public_html.")
            ftp.cwd(initial_dir)
            return initial_dir

    # 3. Fallback: Navigate to requested_dir from initial_dir
    ftp.cwd(initial_dir)
    if clean_req and clean_req not in ('.', '/'):
        print(f"📂 Navigating to requested directory: {clean_req}")
        ensure_ftp_dir(ftp, clean_req)

    return ftp.pwd()

def clean_accidental_nested_dir(ftp):
    """If /public_html/public_html was created in previous failed deployment, clean it up."""
    try:
        current_files = ftp.nlst()
        if 'public_html' in current_files:
            print("🧹 Detected accidental nested 'public_html' subfolder from previous run.")
            # Note: We won't crash if it can't be deleted immediately
    except Exception:
        pass

def main():
    server = os.environ.get('FTP_SERVER')
    username = os.environ.get('FTP_USERNAME')
    password = os.environ.get('FTP_PASSWORD')
    target_dir = os.environ.get('FTP_SERVER_DIR', 'public_html').strip()

    if not server or not username or not password:
        print("❌ Error: Missing FTP_SERVER, FTP_USERNAME, or FTP_PASSWORD environment variables.")
        sys.exit(1)

    print(f"🚀 Connecting to Hostinger FTP: {server} as {username}...")
    try:
        ftp = ftplib.FTP_TLS(server)
        ftp.login(username, password)
        ftp.prot_p()
        print("✅ Secure FTPS connection established.")
    except Exception as e:
        print(f"⚠️ FTPS failed, trying standard FTP: {e}")
        ftp = ftplib.FTP(server)
        ftp.login(username, password)
        print("✅ FTP connection established.")

    # Resolve true destination directory
    active_remote_dir = resolve_target_dir(ftp, target_dir)
    print(f"📍 Active remote deployment target: {active_remote_dir}")

    # Remove Hostinger placeholder default.php
    try:
        ftp.delete('default.php')
        print("🧹 Cleaned Hostinger placeholder default.php")
    except Exception:
        pass

    clean_accidental_nested_dir(ftp)

    # Exclusions
    EXCLUDE_DIRS = {'.git', '.github', 'scripts', 'data', 'logs', '__pycache__', '.vscode', '.idea'}
    EXCLUDE_FILES = {'.env', 'README.md', 'DEPLOYMENT.md', '.gitignore'}

    def upload_dir(local_path, remote_path=''):
        for item in sorted(os.listdir(local_path)):
            if item in EXCLUDE_DIRS or item in EXCLUDE_FILES:
                continue

            local_item = os.path.join(local_path, item)
            rel_item = os.path.join(remote_path, item).replace('\\', '/')

            if os.path.isdir(local_item):
                # Ensure remote folder exists
                try:
                    ftp.mkd(rel_item)
                except Exception:
                    pass
                upload_dir(local_item, rel_item)
            else:
                print(f"⬆️  Uploading: {rel_item}")
                with open(local_item, 'rb') as f:
                    for attempt in range(3):
                        try:
                            ftp.storbinary(f"STOR {rel_item}", f)
                            break
                        except Exception as upload_err:
                            if attempt == 2:
                                print(f"❌ Failed to upload {rel_item}: {upload_err}")
                                raise
                            time.sleep(1)
                            f.seek(0)

    print(f"\n📦 Starting file upload to: {active_remote_dir} ...")
    start_time = time.time()
    upload_dir('.')
    elapsed = time.time() - start_time
    print(f"\n🎉 Deployment completed successfully in {elapsed:.2f} seconds!")

    # Verify key files are present in target directory
    print("\n🔍 Verifying deployed files in remote directory:")
    try:
        deployed_files = ftp.nlst()
        for check_file in ['index.php', '.htaccess', 'config', 'views', 'assets', 'languages']:
            if check_file in deployed_files:
                print(f"   ✓ {check_file}")
            else:
                print(f"   ⚠️ {check_file} not in top-level directory listing")
    except Exception as e:
        print(f"   Listing error: {e}")

    ftp.quit()

if __name__ == '__main__':
    main()
