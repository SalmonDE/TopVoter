# TopVoter [![Build Status](https://travis-ci.org/SalmonDE/TopVoter.svg?branch=master)](https://travis-ci.org/SalmonDE/TopVoter) [![Poggit-CI](https://poggit.pmmp.io/ci.badge/SalmonDE/TopVoter/TopVoter)](https://poggit.pmmp.io/ci/SalmonDE/TopVoter/TopVoter)
Shows a list of voters for your PocketMine-MP Server on a floating text!
--------------------------------------------------------------------------------
**Requirements:**
--------------------------------------------------------------------------------
- An API key of your server from minecraftpocket-servers.com
- An internet connection to connect to the minecraftpocket-servers.com services.

*This plugin has got an built-in updater which always checks for a new version.*
*However, you can disable the auto update function.*
## Preview:

![Preview](https://salmonde.de/MCPE-Plugins/Pictures/TopVoter/Preview.jpg)

**After some time someone voted:**

![Preview2](https://salmonde.de/MCPE-Plugins/Pictures/TopVoter/Preview2.jpg)

## TopVoter API

As of commit [e547488](https://github.com/SalmonDE/TopVoter/commit/e54748840ca6dd9df7f35f6f9d93ab096effcceb) TopVoter provides an API that allows other plugins to access it's data and edit it.

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
