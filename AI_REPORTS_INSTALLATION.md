# AI-Generated Financial Reports - Installation Guide

## Overview
This document provides step-by-step instructions for installing and configuring the AI-Generated Financial Reports feature for the MediTax Connect platform.

## Prerequisites
- PHP 7.4+
- PostgreSQL 12+
- Existing MediTax Connect application
- Healthcare professional user accounts

## Installation Steps

### 1. Database Setup
The database tables are automatically created when the application initializes. The `initializeDatabase()` function in `includes/db.php` will create:
- `ai_financial_reports` - Main reports table
- `ai_report_versions` - Version history table

No manual SQL execution is needed if using the existing setup.

### 2. File Structure
The following files have been added to your project:

```
api/
├── ai-reports.php          (NEW) API endpoints for report operations

pages/
├── ai-reports.php          (NEW) Main UI page for reports dashboard

includes/
├── db.php                  (MODIFIED) Added AI report tables to schema
├── functions.php           (MODIFIED) Added AI report helper functions

index.php                   (MODIFIED) Added routing for new pages/API
```

### 3. Updated Files
The following existing files were modified to support the new feature:

**includes/db.php**
- Added `ai_financial_reports` table definition
- Added `ai_report_versions` table definition

**includes/functions.php**
- Added 10+ new helper functions for AI report operations
- Functions for generation, retrieval, updating, and deletion

**index.php**
- Added `/ai-reports` page route
- Added `/api/ai-reports.php` API route

## Configuration

### AI Model Integration
Currently, the system uses a placeholder AI model (`gpt-4-turbo`). To integrate with a real AI service:

1. **Get API Keys**
   - OpenAI: https://platform.openai.com/
   - Anthropic: https://console.anthropic.com/
   - Other providers as needed

2. **Update Environment Variables**
   Add to your `.env` or environment configuration:
   ```
   AI_PROVIDER=openai
   AI_API_KEY=your_api_key_here
   AI_MODEL=gpt-4-turbo
   ```

3. **Modify AI Integration**
   In `includes/functions.php`, update the `generateAIContent()` function:
   ```php
   function generateAIContent($analysisData) {
       // Call actual AI API here
       // Current implementation returns placeholder content
   }
   ```

### Optional Features

#### PDF Export
To enable PDF export, install a PDF library:
```bash
composer require dompdf/dompdf
```

Then update the `exportAIReportToPDF()` function in `includes/functions.php`.

#### Email Integration
To enable email distribution of reports, configure your email provider:
```php
// Add to environment
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

## Testing

### 1. Verify Installation
1. Log in as a healthcare professional
2. Navigate to `/ai-reports`
3. Page should load without errors

### 2. Create Test Report
1. Click "Generate New Report"
2. Select "Comprehensive Analysis"
3. Choose current year
4. Click "Generate Report"
5. Report should appear in list within a few seconds

### 3. View Report Details
1. Click "View" button on generated report
2. Verify summary, analysis, and recommendations display
3. Check that key metrics are populated

### 4. Test Filtering
1. Select different year in dropdown
2. Verify reports update
3. Test tab filtering (All, Approved, Drafts)

## Troubleshooting

### Issue: Database Tables Not Created
**Solution:** The tables are created on first application load. If they don't exist:
1. Delete any existing database
2. Clear browser cache
3. Refresh the application
4. Check application logs for errors

### Issue: "Unauthorized" Error
**Solution:** Ensure user account is:
- Logged in
- Has role of 'doctor', 'dentist', or 'pharmacy'
- Not an 'accountant' or 'admin' account

### Issue: Report Generation Fails
**Solution:**
1. Check that financial records exist for the selected year
2. Verify database connection is working
3. Check error logs at `error.log`
4. Ensure sufficient server resources

### Issue: Reports Not Displaying
**Solution:**
1. Clear browser cache
2. Check that Tailwind CSS is loading
3. Verify JavaScript is enabled
4. Check browser console for errors

## Upgrade Path

### From Version 1.0 → 1.1
When new features are added:
1. Backup your database
2. Run database migration scripts
3. Update function definitions in `includes/functions.php`
4. Clear application cache
5. Test all functionality

## Performance Considerations

### Database Optimization
For best performance with large numbers of reports:
1. Add indexes (handled in schema):
```sql
CREATE INDEX idx_ai_reports_user_year ON ai_financial_reports(user_id, year);
CREATE INDEX idx_ai_reports_status ON ai_financial_reports(status);
```

2. Archive old reports periodically
3. Monitor query performance

### API Rate Limiting
When integrating with AI services, implement rate limiting:
```php
// Limit reports to 5 per hour
$reports_last_hour = countReportsLastHour($user['id']);
if ($reports_last_hour >= 5) {
    respond(false, null, 'Rate limit exceeded');
}
```

## Backup & Recovery

### Backup Reports
Regular backups should include the `ai_financial_reports` and `ai_report_versions` tables:
```sql
pg_dump -t ai_financial_reports -t ai_report_versions dbname > backup.sql
```

### Restore Reports
```sql
psql dbname < backup.sql
```

## Monitoring & Maintenance

### Monitor Report Generation
Track report generation in logs:
```php
error_log("Report generated: ID=" . $reportId . " for User=" . $userId);
```

### Cleanup Old Versions
Implement periodic cleanup of old report versions:
```php
function cleanupOldVersions() {
    $pdo = getDBConnection();
    $pdo->exec("DELETE FROM ai_report_versions WHERE created_at < NOW() - INTERVAL '1 year'");
}
```

### Database Maintenance
Regular PostgreSQL maintenance:
```sql
VACUUM ANALYZE ai_financial_reports;
REINDEX TABLE ai_financial_reports;
```

## Security Checklist

- [ ] Database tables created successfully
- [ ] Only healthcare professionals can access feature
- [ ] API endpoints properly validate user authorization
- [ ] Financial data is properly sanitized
- [ ] All database queries use prepared statements
- [ ] Error messages don't expose sensitive data
- [ ] Regular backups are configured
- [ ] Access logs are monitored

## Next Steps

1. **Test the installation** using the testing procedures above
2. **Configure AI integration** if using real AI services
3. **Set up email delivery** for report distribution
4. **Configure backups** for database protection
5. **Monitor performance** and optimize as needed
6. **Train users** on using the new feature

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review error logs in the application logs directory
3. Consult `AI_REPORTS_DOCUMENTATION.md` for detailed feature documentation
4. Contact development team for technical support

## Summary

The AI-Generated Financial Reports feature is now installed and ready to use. The feature is backward-compatible and integrates seamlessly with existing MediTax Connect functionality. All database tables are automatically created on first application load.
