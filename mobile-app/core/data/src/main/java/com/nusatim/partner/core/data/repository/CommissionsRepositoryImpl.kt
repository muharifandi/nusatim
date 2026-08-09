package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.domain.repository.CommissionsRepository
import com.nusatim.partner.core.data.repository.BaseRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.CommissionsApiService
import com.nusatim.partner.core.model.dto.CommissionResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class CommissionsRepositoryImpl @Inject constructor(
    private val apiService: CommissionsApiService
) : BaseRepository(), CommissionsRepository {

    override fun getCommissions(
        status: String?,
        page: Int?,
        perPage: Int?
    ): Flow<ResultState<PagedBaseResponse<CommissionResponse>>> = safeNetworkCall {
        apiService.getCommissions(status, page, perPage)
    }

    override fun getCommissionDetail(id: Int): Flow<ResultState<CommissionResponse>> = safeNetworkCall {
        apiService.getCommissionDetail(id).data
    }
}
