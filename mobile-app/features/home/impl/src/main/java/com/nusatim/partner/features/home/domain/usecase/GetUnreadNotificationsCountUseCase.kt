package com.nusatim.partner.features.home.domain.usecase

import com.nusatim.partner.core.domain.repository.NotificationsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.UnreadCountResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetUnreadNotificationsCountUseCase @Inject constructor(
    private val repository: NotificationsRepository
) {
    operator fun invoke(): Flow<ResultState<UnreadCountResponse>> {
        return repository.getUnreadCount()
    }
}
