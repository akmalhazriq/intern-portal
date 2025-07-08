#!/bin/bash

echo "=== SECURITY SCAN FOR INTERN PORTAL ==="
echo "Scanning for potentially malicious files..."
echo ""

# Check for PHP files in upload directories (should not exist)
echo "1. Checking for PHP files in upload directories:"
find src/ -name "*.php" -type f 2>/dev/null
if [ $? -eq 0 ]; then
    echo "WARNING: PHP files found in upload directories!"
else
    echo "✓ No PHP files found in upload directories"
fi
echo ""

# Check for script files
echo "2. Checking for script files in upload directories:"
find src/ \( -name "*.js" -o -name "*.html" -o -name "*.htm" -o -name "*.asp" -o -name "*.jsp" \) -type f 2>/dev/null
if [ $? -eq 0 ]; then
    echo "WARNING: Script files found in upload directories!"
else
    echo "✓ No script files found in upload directories"
fi
echo ""

# Check for executable files
echo "3. Checking for executable files:"
find src/ \( -name "*.exe" -o -name "*.bat" -o -name "*.sh" -o -name "*.com" \) -type f 2>/dev/null
if [ $? -eq 0 ]; then
    echo "WARNING: Executable files found!"
else
    echo "✓ No executable files found"
fi
echo ""

# Check file permissions
echo "4. Checking file permissions:"
echo "Files with execute permissions in src/:"
find src/ -type f -perm -111 2>/dev/null
echo ""

# Check for suspicious content in PHP files
echo "5. Checking for suspicious patterns in PHP files:"
grep -r "eval\|base64_decode\|shell_exec\|system\|exec\|passthru" --include="*.php" . 2>/dev/null | head -10
echo ""

# Check for recently modified files
echo "6. Recently modified files (last 7 days):"
find . -type f -mtime -7 -name "*.php" 2>/dev/null | head -20
echo ""

echo "=== SCAN COMPLETE ==="
echo ""
echo "ACTION ITEMS:"
echo "1. Remove any PHP/script files found in src/ directories"
echo "2. Check recently modified files for unauthorized changes"  
echo "3. Review any suspicious patterns found"
echo "4. Verify .htaccess files are in place in upload directories"
