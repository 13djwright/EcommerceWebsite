#!/usr/bin/python
import random
#go to list of board games on wikipedia and copy most of the data
#into a list. I have 317 unique names of board games.
#now we need to generate the data for each game.
#Just need to generate a random price and quantity

data = [line.rstrip('\r\n') for line in open("./boardgames.txt","r")]
data = list(set(data))

for g in data:
    price = random.uniform(5.0,75.0)
    quantity = random.randint(2,20)
    print "(\"%s\", %0.2f, %d)," % (str(g),price,quantity)




