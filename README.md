# TopVoter [![Build Status](https://travis-ci.org/SalmonDE/TopVoter.svg?branch=master)](https://travis-ci.org/SalmonDE/TopVoter)
Shows a list of voters for your PocketMine-MP Server on a floating text!

## Preview:

![Preview](https://salmonde.de/MCPE-Plugins/Pictures/TopVoter/Preview.jpg)

**After another person has voted:**

![Preview2](https://salmonde.de/MCPE-Plugins/Pictures/TopVoter/Preview2.jpg)

## TopVoter API

As of commit [e547488](https://github.com/SalmonDE/TopVoter/commit/e54748840ca6dd9df7f35f6f9d93ab096effcceb) TopVoter plugin offers an API that allows other plugins to access it's data and edit it.

*Getting the list of voters:*
```
TopVoter::getInstance()->getVoters();
```
*Setting voters on the list:*
```
TopVoter::getInstance()->setVoters($value);
```
*Updating and resending the particles:*
```
TopVoter::getInstance()->updateParticle();
TopVoter::getInstance()->sendParticle();
```
*Using this piece of code, you can see what text will be shown on the particle. `TopVoter::updateParticle()` will return it to you:*
```
$text = TopVoter::getInstance()->updateParticle();
echo $text;
```
