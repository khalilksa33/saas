#!/usr/bin/env python3
"""
Hostinger Subdomain FTPS Deployment Script
Syncs all SaaS files into Hostinger tools/ subdomain directory
"""

import os
import sys
import ftplib
import time

def main():
    server = os.environ.get('FTP_SERVER')
    username = os.environ.get('FTP_USERNAME')
    password = os.environ.get('FTP_PASSWORD')
    target_dir = os.environ.get('FTP_SERVER_DIR', 'tools').strip('/')

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

    # Ensure target directory exists and enter it
    try:
        ftp.cwd(target_dir)
        print(f"📂 Changed to target directory: {target_dir}")
    except Exception:
        print(f"📁 Creating target directory: {target_dir}")
        ftp.mkd(target_dir)
        ftp.cwd(target_dir)

    # Clean default.php if present
    try:
        ftp.delete('default.php')
        print("🧹 Cleaned Hostinger placeholder default.php")
    except Exception:
        pass

    # Exclusions
    EXCLUDE_DIRS = {'.git', '.github', 'scripts', 'data', '__pycache__'}
    EXCLUDE_FILES = {'.env', 'README.md', 'DEPLOYMENT.md', '.gitignore'}

    def upload_dir(local_path, remote_path=''):
        for item in os.listdir(local_path):
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
                    # Retry logic for network blips
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

    print("\n📦 Starting file upload to Hostinger subdomain...")
    start_time = time.time()
    upload_dir('.')
    elapsed = time.time() - start_time
    print(f"\n🎉 Deployment completed successfully in {elapsed:.2f} seconds!")

    ftp.quit()

if __name__ == '__main__':
    main()
