#!/bin/bash

sRepository="gueff/myMVC_module_DB";
sGitUser="gueff";
sGitToken="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";
sBranch="3.2.x";

#--------------------

# update phanlist
. _phanlistcreate.sh

# update
git remote set-url origin "https://$sGitUser:$sGitToken@github.com/$sRepository"
git pull;
