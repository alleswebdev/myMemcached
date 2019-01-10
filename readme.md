task: <br>
You need to implement the library for Memcached.
The library should implement get/set/delete commands at a low level. The Test Driven Development approach should be used in the implementation.

To understand how the client works, you can make a Telnet session like this - it will illustrate a typical client-server communication:
```bash
$ telnet localhost 11211

get key
END
set key 0 3600 3
xyz
STORED
get key
VALUE key 0 3
xyz
END
```
Use Memcached protocol in development: https://github.com/memcached/memcached/blob/master/doc/protocol.txt
Do NOT use external Memcached libraries and extensions.