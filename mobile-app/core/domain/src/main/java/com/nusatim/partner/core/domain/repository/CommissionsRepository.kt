package com.nusatim.partner.core.domain.repository

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.CommissionResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import kotlinx.coroutines.flow.Flow

interface CommissionsRepository {
    fun getCommissions(
        status: String? = null,
        page: Int? = null,
        perPage: Int? = null
    ): Flow<ResultState<PagedBaseResponse<CommissionResponse>>>

    fun getCommissionDetail(id: Int): Flow<ResultState<CommissionResponse>>
}
