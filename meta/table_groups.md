# Table groups

We will use 2 tables..

1. Stores the relation between gid,uid and user_power.
2. Stores individual group accounts.


### table gnetwork

* ID
* GID
* STATUS (bool = 1 is admin)


### table groups

* GID
* GUSERNAME
* GNAME
* GBIO