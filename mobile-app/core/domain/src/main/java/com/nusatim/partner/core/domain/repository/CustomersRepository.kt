package com.nusatim.partner.core.domain.repository

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.CustomerResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import kotlinx.coroutines.flow.Flow

interface CustomersRepository {
    fun getCustomers(
        leadId: Int? = null,
        page: Int? = null,
        perPage: Int? = null
    ): Flow<ResultState<PagedBaseResponse<CustomerResponse>>>

    fun getCustomerDetail(id: Int): Flow<ResultState<CustomerResponse>>

    fun updateCustomer(id: Int, request: Map<String, Any?>): Flow<ResultState<CustomerResponse>>

    fun updateCustomerProgress(id: Int, progress: Int): Flow<ResultState<CustomerResponse>>
}
