# TopVoter [![Build Status](https://travis-ci.org/SalmonDE/TopVoter.svg?branch=master)](https://travis-ci.org/SalmonDE/TopVoter)
Show the voters of your PocketMine server on a floating text!

##Preview

![Preview](https://salmonde.de/MCPE-Plugins/Pictures/TopVoter/Preview.jpg)

**After another person has voted:**

![Preview2](https://salmonde.de/MCPE-Plugins/Pictures/TopVoter/Preview2.jpg)

##API

As of commit e54748840ca6dd9df7f35f6f9d93ab096effcceb TopVoter allows other plugins to access it's data of the voters:

```
TopVoter::getInstance()->getVoters();
```

To set the voters:

```
TopVoter::getInstance()->setVoters($value);
```

You may want to update and resend the particle to the players then:

```
TopVoter::getInstance()->updateParticle();
TopVoter::getInstance()->sendParticle();
```

You'd like to get the text which will be shown on the particle? TopVoter::updateParticle() will return it to you:

```
$text = TopVoter::getInstance()->updateParticle();
echo $text;
```
