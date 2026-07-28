# Database Audit

## Live Database Evidence
The live database was inspected directly using MySQL and the current schema contains four core tables:
- brands
- category
- products
- users

## Schema Summary
### brands
Purpose: Stores brand catalog entries.
Columns:
- brandid (varchar(10), PK)
- brandname (varchar(255), NOT NULL)
- imagelink (varchar(255), default '')
- status (enum('Active','Inactive'))
- created_at (timestamp)
- display_status (tinyint(1))
Relationships:
- One-to-many to category and products.
Indexes:
- Primary key on brandid.
Estimated row count:
- 26 rows (verified from live DB).

### category
Purpose: Stores product categories and links them to brands.
Columns:
- cid (varchar(20), PK)
- cname (varchar(255), NOT NULL)
- cimage (varchar(255), default '')
- brandid (varchar(10), FK to brands.brandid)
- status (enum('Active','Inactive'))
- created_at (timestamp)
- display_status (tinyint(1))
Relationships:
- Many-to-one to brands.
- One-to-many to products.
Indexes:
- Primary key on cid.
- Index on brandid.
- Foreign key fk_category_brand.
Estimated row count:
- 84 rows (verified from live DB).

### products
Purpose: Stores product catalog items.
Columns:
- pid (varchar(30), PK)
- pname (varchar(255), NOT NULL)
- pdescription (text)
- pcat (varchar(20), FK to category.cid)
- brandid (varchar(10), FK to brands.brandid)
- pimage (varchar(255), default '')
- status (enum('Active','Inactive'))
- created_at (timestamp)
- display_status (tinyint(1))
Relationships:
- Many-to-one to category.
- Many-to-one to brands.
Indexes:
- Primary key on pid.
- Index on pcat.
- Index on brandid.
- Foreign keys on pcat and brandid.
Estimated row count:
- 398 rows (verified from live DB).

### users
Purpose: Stores admin authentication credentials.
Columns:
- id (int, PK)
- username (varchar(50), unique)
- password_hash (varchar(255))
Relationships:
- None.
Indexes:
- Primary key, unique username.
Estimated row count:
- 1 row (verified from live DB).

## Findings
### Duplicate brands
Evidence:
- Live DB query returned duplicate brand name: SECUREYE => 2.
Severity: Medium
Impact: Search and merchandising can become inconsistent.

### Duplicate categories
Evidence:
- Live DB contains repeated category names such as ACCESSORIES, CABLES, DVR, NVR, POE, WI-FI MODEL'S.
Severity: High
Impact: Category navigation and grouping become ambiguous.

### Duplicate products
Evidence:
- Live DB contains repeated product names such as 4G BASED CCTV CAMERA (4 rows), WI-FI ROBO (4 rows).
Severity: High
Impact: Product listing pages may show duplicate items and confuse customers.

### Orphan products / categories
Evidence:
- Schema includes foreign keys, but the code does not consistently enforce or verify orphan cleanup before deletes.
Severity: Medium
Impact: Incomplete delete operations can leave inconsistent catalog state.

### Inactive products displayed
Evidence:
- Public pages such as all-products.php explicitly filter to status='Active' and display_status=1, which is good.
- However, the same code does not appear consistently across all pages and the admin screens can still manage data in mixed ways.
Severity: Medium
Impact: The storefront can be inconsistent if admin data is updated manually.

### Missing indexes / schema hardening
Evidence:
- The schema has indexes on brand/category foreign keys but no composite indexes for common product filters such as status + display_status + brand/category.
Severity: Medium
Impact: Catalog queries may become slower as the product table grows.

### Missing foreign key on delete behavior
Evidence:
- The schema defines foreign keys but the delete logic in admin code is handled in application code rather than via cascading rules.
Severity: Medium
Impact: Deletion behavior is more complex and less reliable.

### Normalization issues
Evidence:
- Category names and product names are duplicated in the data, suggesting that the catalog schema is not normalized for merchandising semantics.
Severity: High
Impact: Search and navigation are less reliable.

### Null / empty values
Evidence:
- The schema allows empty image paths and empty descriptions in products and brands.
Severity: Low to Medium
Impact: Empty image paths create broken or blank visual assets.

### Inconsistent IDs
Evidence:
- Brand IDs are generated as B001-like values while category and product IDs are generated based on brand/category prefixes and counters.
Severity: Medium
Impact: ID generation logic is inconsistent and potentially fragile.
