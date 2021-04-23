import service from '@/utils/request'

// @Summary 删除OperationRecord
export const deleteOperationRecord = (id) => {
    return service({
        url: `/accessLog/${id}`,
        method: 'DELETE',
    })
}

// @Summary 批量删除OperationRecord
export const deleteOperationRecordByIds = (id) => {
    return service({
        url: `/accessLog/${id}`,
        method: 'DELETE',
    })
}

// @Summary 分页获取OperationRecord列表
export const getOperationRecordList = (params) => {
    return service({
        url: "/accessLog/list",
        method: 'GET',
        params
    })
}