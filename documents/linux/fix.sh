#!/bin/bash

export OPENSSL_CFLAGS="/usr/local/opt/openssl/include/"
export OPENSSL_LIBS="/usr/local/opt/openssl/lib/"
export KERBEROS_CFLAGS="/usr/local/opt/krb5/include/"
export KERBEROS_LIBS="/usr/local/opt/krb5/lib/"
export PATH="/usr/local/opt/libiconv/bin:$PATH"
# For compilers to find libiconv you may need to set:
export LDFLAGS="-L/usr/local/opt/libiconv/lib"
export CPPFLAGS="-I/usr/local/opt/libiconv/include"


