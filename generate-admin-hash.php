<?php
// Generate bcrypt hash for password "admin123"
$password = "admin123";
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
echo "\n";
echo "SQL to insert admin user:\n";
echo "===============================================\n";
echo "INSERT INTO users (name, email, phone, password, role) VALUES (\n";
echo "    'Admin',\n";
echo "    'admin@fspoltd.rw',\n";
echo "    '+250 785 723 677',\n";
echo "    '" . $hash . "',\n";
echo "    'admin'\n";
echo ") ON CONFLICT (email) DO UPDATE SET \n";
echo "  password = '" . $hash . "',\n";
echo "  role = 'admin';\n";
echo "===============================================\n";
?>
