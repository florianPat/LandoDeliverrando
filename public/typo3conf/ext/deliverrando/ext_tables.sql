CREATE TABLE tx_deliverrando_domain_model_product (
  uid int(11) unsigned DEFAULT 0 NOT NULL auto_increment,
  pid int(11) DEFAULT 0 NOT NULL,
  name varchar(255) DEFAULT '' NOT NULL,
  description varchar(255) DEFAULT '' NOT NULL,
  quantity int(11) DEFAULT 0 NOT NULL,
  deliverytime int(11) DEFAULT 0 NOT NULL,
  --NOTE: foreign uid for 1-n relation
  delieverrando int(11) DEFAULT 0 NOT NULL,
  --NOTE: categories objectStorage-member (its a counter in the db)
  categories int(11) DEFAULT 0 NOT NULL,

  crdate int(11) DEFAULT 0 NOT NULL,
  tstamp int(11) unsigned DEFAULT 0 NOT NULL,
  deleted smallint(4) unsigned DEFAULT 0 NOT NULL,
  hidden smallint(4) unsigned DEFAULT 0 NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

CREATE TABLE tx_deliverrando_domain_model_delieverrando(
    uid int(11) unsigned DEFAULT 0 NOT NULL auto_increment,
    pid int(11) DEFAULT 0 NOT NULL,

    name varchar(255) DEFAULT '' NOT NULL,
    --NOTE: products objectStorage-member (its a counter in the db)
    products int(11) unsigned DEFAULT 0 NOT NULL,
    --NOTE: uid of the fe-userGroup (FrontentUserGroup-Object in the domain model)
    user_group int(11) unsigned NOT NULL,
    address varchar(255) DEFAULT '' NOT NULL,

    crdate int(11) DEFAULT 0 NOT NULL,
    tstamp int(11) unsigned DEFAULT 0 NOT NULL,
    deleted smallint(4) unsigned DEFAULT 0 NOT NULL,
    hidden smallint(4) unsigned DEFAULT 0 NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_deliverrando_domain_model_category (
    uid int(11) unsigned DEFAULT 0 NOT NULL auto_increment,
    pid int(11) DEFAULT 0 NOT NULL,

    name varchar(255) DEFAULT '' NOT NULL,

    crdate int(11) DEFAULT 0 NOT NULL,
    tstamp int(11) unsigned DEFAULT 0 NOT NULL,
    deleted smallint(4) unsigned DEFAULT 0 NOT NULL,
    hidden smallint(4) unsigned DEFAULT 0 NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

--NOTE: table for m-n relation (local = product & foreign = category. Sorting muss gesetzt werden, sorting_foreign aber nicht)
--NOTE: Die naming convetion besagt, dass man die DB tx_pluginname_localTable_foreignTable_mm nennen sollte!)
CREATE TABLE tx_deliverrando_product_category_mm(
    uid_local int(11) unsigned NOT NULL,
    uid_foreign int(11) unsigned NOT NULL,
    sorting int(11) unsigned NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT 0 NOT NULL,

    crdate int(11) DEFAULT 0 NOT NULL,
    tstamp int(11) unsigned DEFAULT 0 NOT NULL,
    deleted smallint(4) unsigned DEFAULT 0 NOT NULL,
    hidden smallint(4) unsigned DEFAULT 0 NOT NULL
);

CREATE TABLE tx_deliverrando_domain_model_person(
    uid int(11) unsigned DEFAULT 0 NOT NULL auto_increment,
    pid int(11) DEFAULT 0 NOT NULL,

    name varchar(255) DEFAULT '' NOT NULL,
    address varchar(255) DEFAULT '' NOT NULL,
    telephonenumber varchar(16) DEFAULT '' NOT NULL,
    password varchar(255) DEFAULT '' NOT NULL,
    email varchar(255) DEFAULT '' NOT NULL,

    crdate int(11) DEFAULT 0 NOT NULL,
    tstamp int(11) unsigned DEFAULT 0 NOT NULL,
    deleted smallint(4) unsigned DEFAULT 0 NOT NULL,
    hidden smallint(4) unsigned DEFAULT 0 NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_deliverrando_domain_model_order(
    uid int(11) DEFAULT 0 NOT NULL auto_increment,
    pid int(11) DEFAULT 0 NOT NULL,

    --foreign uid for person in db, Person in domain
    person int(11) unsigned DEFAULT 0 NOT NULL,
    --products object-storage in domain, counter in db
    products int(11) unsigned DEFAULT 0 NOT NULL,
    productquantities varchar(255) DEFAULT '' NOT NULL,
    productprogress varchar(255) DEFAULT '' NOT NULL,
    deliverytime int(11) DEFAULT 0 NOT NULL,

    crdate int(11) DEFAULT 0 NOT NULL,
    tstamp int(11) unsigned DEFAULT 0 NOT NULL,
    deleted smallint(4) unsigned DEFAULT 0 NOT NULL,
    hidden smallint(4) unsigned DEFAULT 0 NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_deliverrando_order_product_mm(
    uid_local int(11) unsigned NOT NULL,
    uid_foreign int(11) unsigned NOT NULL,
    sorting int(11) unsigned NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT 0 NOT NULL,

    crdate int(11) DEFAULT 0 NOT NULL,
    tstamp int(11) unsigned DEFAULT 0 NOT NULL,
    deleted smallint(4) unsigned DEFAULT 0 NOT NULL,
    hidden smallint(4) unsigned DEFAULT 0 NOT NULL,
);

CREATE TABLE sys_template(
    plugin_for_template varchar(255) DEFAULT '' NOT NULL
);

/* NOTE: Dieser table wird nur erstellt, wenn man im install tool unter Database Analyzer
 * die Datenbank mit (dieser) Spezifikation vergleicht, und dann den table erstellt.
 */