package com.nusatim.partner.features.home.domain.usecase

import com.nusatim.partner.core.domain.repository.NotificationsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.NotificationResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class MarkNotificationAsReadUseCase @Inject constructor(
    private val repository: NotificationsRepository
) {
    operator fun invoke(id: String): Flow<ResultState<NotificationResponse>> {
        return repository.markAsRead(id)
    }
}
