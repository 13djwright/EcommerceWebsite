#!/bin/bash

mysql -u root -pbudget project < dropTables.sql;
mysql -u root -pbudget project < createTables.sql;
mysql -u root -pbudget project < games.sql;

