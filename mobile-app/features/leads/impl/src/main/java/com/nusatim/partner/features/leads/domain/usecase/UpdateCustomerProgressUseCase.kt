package com.nusatim.partner.features.leads.domain.usecase

import com.nusatim.partner.core.domain.repository.CustomersRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.CustomerResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class UpdateCustomerProgressUseCase @Inject constructor(
    private val repository: CustomersRepository
) {
    operator fun invoke(id: Int, progress: Int): Flow<ResultState<CustomerResponse>> {
        return repository.updateCustomerProgress(id, progress)
    }
}
