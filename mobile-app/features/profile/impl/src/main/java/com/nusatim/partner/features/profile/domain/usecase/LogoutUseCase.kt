package com.nusatim.partner.features.profile.domain.usecase

import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.common.security.SessionManager
import com.nusatim.partner.core.model.ResultState
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.onEach
import javax.inject.Inject

class LogoutUseCase @Inject constructor(
    private val authRepository: AuthRepository,
    private val sessionManager: SessionManager
) {
    operator fun invoke(): Flow<ResultState<String>> {
        return authRepository.logout().onEach { result ->
            if (result is ResultState.Success || result is ResultState.Error) {
                // Hapus token lokal terlepas dari sukses/gagal API
                sessionManager.clearSession()
            }
        }
    }
}
