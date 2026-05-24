# Mauvaises pratiques simulées

Ce fichier contient uniquement des exemples fictifs pour discussion.

```text
DEMO_STRIPE_SECRET=sk_test_eventsecure_demo_000000000000
DEMO_JWT_SECRET=local_lab_secret_do_not_use
DEMO_DATABASE_PASSWORD=app
```

Questions:

- Pourquoi ces valeurs ne doivent-elles pas être dans Git ?
- Comment distinguer secret réel, secret de démo et configuration locale ?
- Quelles traces doivent être purgées dans les logs et tickets ?
