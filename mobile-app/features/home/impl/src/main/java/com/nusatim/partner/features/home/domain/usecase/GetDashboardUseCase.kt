package com.nusatim.partner.features.home.domain.usecase

import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.DashboardResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetDashboardUseCase @Inject constructor(
    private val repository: AuthRepository
) {
    operator fun invoke(): Flow<ResultState<DashboardResponse>> = repository.getDashboard()
}
