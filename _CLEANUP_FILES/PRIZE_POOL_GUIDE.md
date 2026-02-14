# Competition Creation - Prize Pool Guide

## Prize Pool Validation Rules

### Rule 1: Total Prize Pool = Sum of Cash Prizes ONLY
- **Cash prizes** count toward the total
- **Non-cash prizes** (Certificate, Gift Box, Trophy, Other) do NOT count
- The form displays "Cash prizes total" showing the real-time sum

**Example:**
```
Prizes:
  1st Place - Cash: 5,000
  2nd Place - Cash: 3,000
  3rd Place - Certificate: "Gold Certificate"
  
Total Prize Pool MUST be: 8,000 (5000 + 3000)
NOT 8,000 + description values
```

### Rule 2: Cash Prize Amounts Must Be > 1
- Minimum cash prize amount is 2
- Use numbers only (no commas or symbols)

### Rule 3: Non-Cash Prizes Require Description
- Certificate: "Gold Certificate"
- Gift Box: "Premium Gift Box with Awards"
- Trophy: "Crystal Trophy"
- Other: Any description

## Example Form Data

### Scenario 1: Mixed Prizes (Most Common)
```
Total Prize Pool: 10000

Prizes:
  Rank: 1st          Type: Cash              Amount: 5000      Description: -
  Rank: 2nd          Type: Cash              Amount: 3000      Description: -
  Rank: 3rd          Type: Cash              Amount: 2000      Description: -
  Rank: -            Type: Certificate       Amount: -         Description: Gold Certificate
  Rank: -            Type: Gift Box          Amount: -         Description: Premium Photography Kit
```

### Scenario 2: Grand Prize + Honorable Mentions
```
Total Prize Pool: 15000

Prizes:
  Rank: Grand Prize        Type: Cash              Amount: 10000     Description: -
  Rank: 1st                Type: Cash              Amount: 3000      Description: -
  Rank: 2nd                Type: Cash              Amount: 2000      Description: -
  Rank: Honorable Mention  Type: Certificate       Amount: -         Description: Participation Certificate
```

## Testing Steps

1. Go to: http://127.0.0.1:8000/admin/competitions/create
2. Fill in all required fields (Title, Theme, Submission Deadline)
3. Add prizes using "Add Prize" button
4. Ensure Total Prize Pool = Sum of cash prizes
5. Submit form
6. Verify success message and redirect to competition list
