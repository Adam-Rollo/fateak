-- Keys[1] is uri, argv[1] is data, argv[2] is timestamp
local data = cjson.decode(ARGV[1])
redis.call('SADD', 'foptimizer.uri', KEYS[1])
local result = ''

for group_name, group in pairs(data) do
    for item_name, item_info in pairs(group) do
        local itemkey = group_name .. ':' .. item_name
        redis.call('SADD', 'foptimizer.uri:' .. KEYS[1], itemkey)
        -- Max time
        result = result .. '-------' .. itemkey
        local max_time = redis.call('HGET', 'foptimizer.info:' .. itemkey, 'max_time') or 0 
        if tonumber(item_info.max.time) > tonumber(max_time) then
            redis.call('HSET', 'foptimizer.info:' .. itemkey, 'max_time', tonumber(item_info.max.time))
        end
        -- Mim time
        local min_time = redis.call('HGET', 'foptimizer.info:' .. itemkey, 'min_time') or 999999
        if tonumber(item_info.min.time) < tonumber(min_time) then
            redis.call('HSET', 'foptimizer.info:' .. itemkey, 'min_time', tonumber(item_info.min.time))
        end
        -- Max Memory
        local max_memory = redis.call('HGET', 'foptimizer.info:' .. itemkey, 'max_memory') or 0
        if tonumber(item_info.max.memory) > tonumber(max_time) then
            redis.call('HSET', 'foptimizer.info:' .. itemkey, 'max_memory', tonumber(item_info.max.memory))
        end
        -- Mim Memory
        local min_memory = redis.call('HGET', 'foptimizer.info:' .. itemkey, 'min_memory') or 999999
        if tonumber(item_info.min.memory) > tonumber(min_memory) then
            redis.call('HSET', 'foptimizer.info:' .. itemkey, 'min_memory', tonumber(item_info.min.memory))
        end
        -- Total time
        local total_time = redis.call('HGET', 'foptimizer.info:' .. itemkey, 'total_time') or 0 
        total_time = (total_time * 9 + tonumber(item_info.total.time)) / 10
        redis.call('HSET', 'foptimizer.info:' .. itemkey, 'total_time', total_time)
        -- Total memory
        local total_memory = redis.call('HGET', 'foptimizer.info:' .. itemkey, 'total_memory') or 0
        total_memory = (total_memory * 9 + tonumber(item_info.total.memory)) / 10
        redis.call('HSET', 'foptimizer.info:' .. itemkey, 'total_memory', total_memory)
        -- Average time
        local average_time = redis.call('HGET', 'foptimizer.info:' .. itemkey, 'average_time') or 0
        average_time = (average_time * 9 + tonumber(item_info.average.time)) / 10
        redis.call('HSET', 'foptimizer.info:' .. itemkey, 'average_time', average_time)
        -- Average memory
        local average_memory = redis.call('HGET', 'foptimizer.info:' .. itemkey, 'average_memory') or 0
        average_memory = (average_memory * 9 + tonumber(item_info.average.memory)) / 10
        redis.call('HSET', 'foptimizer.info:' .. itemkey, 'average_memory', average_memory)    
        -- updated time
        redis.call('HSET', 'foptimizer.info:' .. itemkey, 'updated_time', ARGV[2]) 
    end
end

return result
