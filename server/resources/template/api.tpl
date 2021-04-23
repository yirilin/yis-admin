import service from '@/utils/request'

//创建{{Template}}数据
export const create{{Template}} = (data) => {
    return service({
        url: "/{{apiName}}",
        method: 'POST',
        data
    })
}

//根据ID或IDS 删除{{Template}}数据
export const delete{{Template}} = (id) => {
    return service({
        url: `/{{apiName}}/${id}`,
        method: 'DELETE',
    })
}

//更新{{Template}}数据
export const update{{Template}} = (id, data) => {
    return service({
        url: `/{{apiName}}/${id}`,
        method: 'PUT',
        data
    })
}

//根据ids查找{{Template}}数据
export const find{{Template}} = (type) => {
    return service({
        url: `/{{apiName}}/find/${type}`,
        method: 'GET',
    })
}

//分页获取{{Template}}列表
export const get{{Template}}List = (params) => {
    return service({
        url: "/{{apiName}}/list",
        method: 'GET',
        params
    })
}
