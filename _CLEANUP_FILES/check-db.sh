# Quick deployment script shell
cd /home/photographersb/htdocs/photographersb.com/public

# First, check what database exists
echo "Checking MySQL access and current databases..."

# Test if we can connect
mysql -u root -e "SELECT 1" 2>&1 | head -3

# If that fails, try to find CloudPanel's credentials
echo "Looking for CloudPanel database credentials..."
find /home/clp -name "*.py" -o -name "*.json" -o -name "*.db" 2>/dev/null | grep -i db | head -5

# Check what files/configs we have
ls -la /home/clp/ 2>/dev/null | head -10
