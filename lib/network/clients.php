<?php

function isAllowedClient($whitelist) {
  if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
    return true;
  } else {
    foreach($whitelist as $i) {
      $wildcardPos = strpos($i, "*");
      if($wildcardPos !== false && substr($_SERVER['REMOTE_ADDR'], 0, $wildcardPos) . "*" == $i) {
        return true;
      }
    }
  }
  return false;
}

?>
