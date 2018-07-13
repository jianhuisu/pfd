<?php
/**
 * User: sujianhui
 * Date: 2017-12-29
 * Time: 16:18
 */

 together form the timeout parameter. The
 timeout is an upper bound on the amount of time
 elapsed before socket_select return.
 tv_sec may be zero , causing
 socket_select to return immediately. This is useful
 for polling. If tv_sec is NULL (no timeout),
socket_select can block indefinitely