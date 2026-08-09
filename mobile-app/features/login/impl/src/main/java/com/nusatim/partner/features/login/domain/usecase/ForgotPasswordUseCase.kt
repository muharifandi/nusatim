package com.nusatim.partner.features.login.domain.usecase

import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.model.ResultState
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class ForgotPasswordUseCase @Inject constructor(
    private val repository: AuthRepository
) {
    operator fun invoke(email: String): Flow<ResultState<String>> {
        return repository.forgotPassword(email)
    }
}
