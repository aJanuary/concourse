<?php

require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(__DIR__ . '/MemberSource.php');

class DbMemberSource implements MemberSource {
  public function members_for_email(string $email): array {
    return db_get_members_by_email($email);
  }

  public function search(string $query): array {
    return db_search_members($query, MEMBER_SOURCE_SEARCH_LIMIT);
  }
}
