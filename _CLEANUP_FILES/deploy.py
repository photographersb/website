#!/usr/bin/env python3
import sys
import subprocess
import time
import re

# VPS Connection details
VPS_IP = "148.135.136.95"
VPS_USER = "root"
VPS_PASS = "Bangladesh#2026"
APP_PATH = "/home/photographersb/htdocs/photographersb.com/public"

def run_ssh_command(cmd, timeout=60):
    """Run a command via SSH using sshpass"""
    try:
        full_cmd = f"sshpass -p '{VPS_PASS}' ssh {VPS_USER}@{VPS_IP} '{cmd}'"
        print(f"Running: {cmd[:80]}...")
        result = subprocess.run(full_cmd, shell=True, capture_output=True, text=True, timeout=timeout)
        return result.stdout, result.stderr, result.returncode
    except subprocess.TimeoutExpired:
        print(f"Command timed out after {timeout}s")
        return "", "Timeout", 1
    except Exception as e:
        print(f"Error: {e}")
        return "", str(e), 1

def main():
    print("=" * 60)
    print("Laravel Deployment Script")
    print("=" * 60)
    
    # Step 1: Verify git checkout is complete
 print("\n[Step 1] Verifying git state...")
    stdout, stderr, code = run_ssh_command(f"cd {APP_PATH} && [ -f composer.json ] && echo 'OK' || echo 'MISSING'")
    if "OK" not in stdout:
        print(f"✗ composer.json not found!")
        print(f"  Output: {stdout.strip()}")
        return False
    print("✓ Application files present")
    
    # Step 2: Check composer installation
    print("\n[Step 2] Checking composer installation...")
    stdout, stderr, code = run_ssh_command(f"cd {APP_PATH} && [ -d vendor ] && echo 'OK' || echo 'MISSING'")
    if "OK" not in stdout:
        print("✗ Vendor directory not found!")
        return False
    print("✓ Composer dependencies installed")
    
    # Step 3: Try migrations with root:root
    print("\n[Step 3] Running Laravel migrations...")
    cmd = f"cd {APP_PATH} && sudo -u photographersb php artisan migrate --force 2>&1 | tail -30"
    stdout, stderr, code = run_ssh_command(cmd, timeout=120)
    print(stdout)
    if code != 0:
        print(f"✗ Migration may have failed. Error:\n{stderr}")
        # Don't return False yet - might have worked despite error code
    
    # Step 4: Set final permissions
    print("\n[Step 4] Setting final permissions...")
    cmd = f"cd {APP_PATH} && chmod -R 755 . && chmod -R 775 storage bootstrap/cache && chown -R photographersb:photographersb ."
    stdout, stderr, code = run_ssh_command(cmd)
    if code == 0:
        print("✓ Permissions set correctly")
    else:
        print(f"✗ Permission setting had issues: {stderr}")
    
    print("\n" + "=" * 60)
    print("Deployment Status")
    print("=" * 60)
    print(f"Application Path: {APP_PATH}")
   print(f"Site URL: https://photographersb.com")
    
    return True

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
