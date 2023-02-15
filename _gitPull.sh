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
