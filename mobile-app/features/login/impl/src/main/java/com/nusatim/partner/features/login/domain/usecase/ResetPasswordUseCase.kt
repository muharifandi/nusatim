package com.nusatim.partner.features.login.domain.usecase

import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.model.ResultState
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class ResetPasswordUseCase @Inject constructor(
    private val repository: AuthRepository
) {
    operator fun invoke(request: Map<String, String>): Flow<ResultState<String>> {
        return repository.resetPassword(request)
    }
}
