#!/bin/bash

echo "INSERT INTO \`user\`(\`name\`, \`email\`, \`password\`) VALUES('$1', '$1@gmail.com', sha1('123456')); INSERT INTO \`playlist\`(\`user_id\`, \`name\`) VALUES(last_insert_id(), '$1');" | mysql -u youbetter -pyoubetter youbetter
