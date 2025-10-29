-- Create the two databases
CREATE DATABASE IF NOT EXISTS `members`;
CREATE DATABASE IF NOT EXISTS `conreg`;

-- Grant privileges to the dba user on both databases
GRANT ALL PRIVILEGES ON `members`.* TO 'dba'@'%';
GRANT ALL PRIVILEGES ON `conreg`.* TO 'dba'@'%';

CREATE TABLE IF NOT EXISTS `conreg`.`conreg_members` (
  `mid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key: Unique member ID.',
  `eid` int(11) NOT NULL COMMENT 'Foreign Key: Event ID.',
  `lead_mid` int(11) NOT NULL DEFAULT 0 COMMENT 'Member ID of lead member (to link groups who register together)',
  `random_key` int(11) NOT NULL DEFAULT 0 COMMENT 'A random generated key to provide some security in payment URL',
  `member_no` int(11) DEFAULT 0 COMMENT 'Member number - assigned once approved.',
  `language` varchar(8) NOT NULL COMMENT 'Member language',
  `member_type` varchar(16) NOT NULL DEFAULT '' COMMENT 'Type of membership.',
  `days` varchar(16) DEFAULT '' COMMENT 'Selected day codes separated by |',
  `communication_method` varchar(1) NOT NULL DEFAULT '' COMMENT 'E=Electronic, P=Paper, B=Both.',
  `is_approved` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Set to 1 if member wishes to volunteer to help.',
  `approver_uid` int(11) DEFAULT 0 COMMENT 'User ID of the admin who approved the member.',
  `first_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'First name of the member.',
  `last_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Last name of the member.',
  `badge_name` varchar(255) DEFAULT '' COMMENT 'Badge name of the member.',
  `badge_type` varchar(4) NOT NULL DEFAULT '' COMMENT 'Type of Badge (e.g. Attending, Guest, Committee, Dealer).',
  `display` varchar(1) NOT NULL DEFAULT '' COMMENT 'Display in membership list - F=Full Name, B=Badge Name, N=Not at all.',
  `email` varchar(255) DEFAULT '' COMMENT 'Email address of the member (only required for first member).',
  `street` varchar(255) DEFAULT '' COMMENT 'Street address of the member.',
  `street2` varchar(255) DEFAULT '' COMMENT 'Street address line 2 of the member.',
  `city` varchar(255) DEFAULT '' COMMENT 'Town/city address of the member.',
  `county` varchar(255) DEFAULT '' COMMENT 'State/county of the member.',
  `postcode` varchar(255) DEFAULT '' COMMENT 'Postcode of the member.',
  `country` varchar(2) DEFAULT '' COMMENT 'Country of the member.',
  `phone` varchar(255) DEFAULT '' COMMENT 'Phone number of the member.',
  `birth_date` datetime DEFAULT NULL COMMENT 'SQL datetime for the member''s date of birth.',
  `age` int(11) DEFAULT 0 COMMENT 'Age of member.',
  `is_volunteer` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Set to 1 if member wishes to volunteer to help.',
  `member_price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Price for this member (excluding add-on).',
  `member_total` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total price for member (including add-on).',
  `is_paid` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Set to 1 if payment complete.',
  `payment_method` varchar(255) NOT NULL DEFAULT '' COMMENT 'How payment received (Stripe, PayPal, etc).',
  `payment_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total amount received for all members in group.',
  `payment_id` varchar(255) DEFAULT '' COMMENT 'Payment confirmation code from Stripe or other payment system.',
  `comment` varchar(1024) DEFAULT '' COMMENT 'Admin comments about member.',
  `add_on` varchar(255) DEFAULT '' COMMENT 'Optional addons to membership.',
  `add_on_price` decimal(10,2) DEFAULT 0.00 COMMENT 'Price of add-on for this member.',
  `add_on_info` varchar(255) DEFAULT '' COMMENT 'Extra information about add-on.',
  `extra_flag1` tinyint(4) DEFAULT 0 COMMENT 'Optional user defined extra checkbox.',
  `extra_flag2` tinyint(4) DEFAULT 0 COMMENT 'Optional user defined extra checkbox.',
  `join_date` int(11) NOT NULL DEFAULT 0 COMMENT 'Unix timestamp for the date and time registered.',
  `update_date` int(11) NOT NULL DEFAULT 0 COMMENT 'Unix timestamp for the date and time last updated.',
  `login_exp_date` int(11) NOT NULL DEFAULT 0 COMMENT 'Unix timestamp for the date and time member was checked in.',
  `is_checked_in` tinyint(4) DEFAULT 0 COMMENT 'True if member has checked in at convention.',
  `check_in_date` int(11) NOT NULL DEFAULT 0 COMMENT 'Unix timestamp for the date and time member was checked in.',
  `check_in_by` int(11) DEFAULT NULL COMMENT 'The Drupal user ID who checked the member in.',
  `is_deleted` tinyint(4) DEFAULT 0 COMMENT 'True if member is deleted.',
  PRIMARY KEY (`mid`),
  KEY `name` (`last_name`(191),`first_name`(191)),
  KEY `lead_mid` (`lead_mid`)
) ENGINE=InnoDB AUTO_INCREMENT=450 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores member details for convention registrations.'

FLUSH PRIVILEGES;
