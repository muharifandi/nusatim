package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.domain.repository.CustomersRepository
import com.nusatim.partner.core.data.repository.BaseRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.CustomersApiService
import com.nusatim.partner.core.model.dto.CustomerResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class CustomersRepositoryImpl @Inject constructor(
    private val apiService: CustomersApiService
) : BaseRepository(), CustomersRepository {

    override fun getCustomers(
        leadId: Int?,
        page: Int?,
        perPage: Int?
    ): Flow<ResultState<PagedBaseResponse<CustomerResponse>>> = safeNetworkCall {
        apiService.getCustomers(leadId, page, perPage)
    }

    override fun getCustomerDetail(id: Int): Flow<ResultState<CustomerResponse>> = safeNetworkCall {
        apiService.getCustomerDetail(id).data
    }

    override fun updateCustomer(id: Int, request: Map<String, Any?>): Flow<ResultState<CustomerResponse>> = safeNetworkCall {
        apiService.updateCustomer(id, request).data
    }

    override fun updateCustomerProgress(id: Int, progress: Int): Flow<ResultState<CustomerResponse>> = safeNetworkCall {
        apiService.updateCustomerProgress(id, mapOf("progress" to progress)).data
    }
}
