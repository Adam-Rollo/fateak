-- Keys[1] is ip, argv[1] is time duration, argv[2] is times
local key = 'rate:limiting:' .. KEYS[1]
local rate_count = redis.call('LLEN', key)
local rate_max = tonumber(ARGV[2])
-- Because of 'deterministic commands', we cannot use redis.call('TIME')[1]
local ctime = tonumber(ARGV[3])
if rate_count < rate_max then
    redis.call('LPUSH', key, ctime)
    return 'Y'
else
    local time = redis.call('LINDEX', key, -1)
    if ctime - time < tonumber(ARGV[1]) then
        return 'N'
    else
        redis.call('LPUSH', key, ctime)
        redis.call('LTRIM', key, 0, rate_max - 1)
        return 'Y'
    end
end
