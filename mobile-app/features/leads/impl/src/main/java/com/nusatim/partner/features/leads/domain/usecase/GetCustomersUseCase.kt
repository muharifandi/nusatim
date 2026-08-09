package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.CustomersRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.CustomerResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetCustomersUseCase @Inject constructor(
    private val repository: CustomersRepository
) {
    operator fun invoke(page: Int? = null, perPage: Int? = null): Flow<ResultState<PagedBaseResponse<CustomerResponse>>> {
        return repository.getCustomers(page, perPage)
    }
}
