<?php
if ($case !== 1 && $case !== 2 && $case !== 3 && $case !== 4) {
  $groups = getGroups($mysqli, $groupTableName);
  $data[] = [];
  $case = 1;
  $message = 'Bitte wähle ein Thema aus';
};
