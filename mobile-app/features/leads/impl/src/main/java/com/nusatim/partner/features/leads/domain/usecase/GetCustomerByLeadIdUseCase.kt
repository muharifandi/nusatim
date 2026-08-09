package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.CustomersRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.CustomerResponse
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map
import javax.inject.Inject

class GetCustomerByLeadIdUseCase @Inject constructor(
    private val repository: CustomersRepository
) {
    operator fun invoke(leadId: Int): Flow<ResultState<CustomerResponse>> {
        return repository.getCustomers(leadId = leadId).map { result ->
            when (result) {
                is ResultState.Loading -> ResultState.Loading
                is ResultState.Success -> {
                    val customer = result.data.data.firstOrNull()
                    if (customer != null) {
                        ResultState.Success(customer)
                    } else {
                        ResultState.Error("Customer tidak ditemukan untuk lead ini.")
                    }
                }
                is ResultState.Error -> ResultState.Error(result.message)
            }
        }
    }
}
