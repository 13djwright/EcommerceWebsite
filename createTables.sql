CREATE TABLE IF NOT EXISTS users(
role VARCHAR(20) DEFAULT "CUSTOMER",
firstName VARCHAR(30) NOT NULL,
lastName VARCHAR(30) NOT NULL,
password VARCHAR(30) NOT NULL,
email VARCHAR(40) NOT NULL PRIMARY KEY,
address VARCHAR(100) NOT NULL,
zipCode INTEGER(5) NOT NULL,
state VARCHAR(2) NOT NULL,
loggedIn BOOLEAN NOT NULL DEFAULT 0,
dateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products(
id INTEGER(10) PRIMARY KEY AUTO_INCREMENT NOT NULL,
price DECIMAL(18,2) NOT NULL,
name VARCHAR(100) NOT NULL,
quantity INTEGER(6) NOT NULL,
promoDiscount INTEGER(3) NOT NULL DEFAULT 0,
promoFrom DATE,
promoTo DATE
);


CREATE TABLE IF NOT EXISTS orders(
id INTEGER(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
dateOrdered DATE DEFAULT NULL,
dateShipped DATE DEFAULT NULL,
userEmail VARCHAR(40) NOT NULL,
FOREIGN KEY (userEmail) REFERENCES users(email)
);

CREATE TABLE IF NOT EXISTS orderDetails(
id INTEGER(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
orderID INTEGER(10) NOT NULL,
productID INTEGER(10) NOT NULL,
quantity INTEGER(3) NOT NULL,
FOREIGN KEY (orderID) REFERENCES orders(id),
FOREIGN KEY (productID) REFERENCES products(id)
);

INSERT IGNORE INTO users(role, firstName, lastName, password, email, address, zipCode, state) VALUES 
("MANAGER", "Devin", "Wright", "cspass", "13djwright@gmail.com", "3500 Beaver Place Road", 40503, "KY"),
("STAFF", "Brandon", "Stockwell", "cspass", "bgs1292@gmail.com", "Tates Creek Road", 40502, "KY");

INSERT INTO orders(userEmail) VALUES ("13djwright@gmail.com"), ("bgs1292@gmail.com");
