local ov = redis.call('get', KEYS[1])
ov = ov + ARGV[1]
redis.call('set', KEYS[1], ov)
return ov
