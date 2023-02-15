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
