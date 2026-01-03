#!/bin/bash
# Quick start script to test the migration

echo "🚀 Starting PHP 7.4 Migration Proof of Concept Test"
echo ""

# Check PHP version
echo "Step 1: Checking PHP version..."
php -v | head -n 1

# Check PDO
echo ""
echo "Step 2: Checking PDO MySQL..."
php -m | grep pdo_mysql && echo "✓ PDO MySQL available" || echo "✗ PDO MySQL not available"

# Run comprehensive tests
echo ""
echo "Step 3: Running comprehensive test suite..."
echo "Opening test_migration.php in your browser..."
echo ""

# Run the test and save results
php test_migration.php > test_results.html 2>&1

if [ $? -eq 0 ]; then
    echo "✅ Tests completed successfully!"
    echo ""
    echo "Results saved to: test_results.html"
    echo ""
    echo "You can view the results by opening test_results.html in your browser"
    echo "or run: xdg-open test_results.html"
else
    echo "❌ Some tests failed. Check test_results.html for details."
fi

echo ""
echo "📖 For detailed documentation, see: MIGRATION_POC.md"
