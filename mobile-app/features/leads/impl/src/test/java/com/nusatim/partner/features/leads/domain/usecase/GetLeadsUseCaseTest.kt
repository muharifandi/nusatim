package com.nusatim.partner.features.leads.domain.usecase

import app.cash.turbine.test
import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.LeadResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import io.mockk.every
import io.mockk.mockk
import kotlinx.coroutines.flow.flowOf
import kotlinx.coroutines.test.runTest
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

class GetLeadsUseCaseTest {

    private lateinit var repository: LeadsRepository
    private lateinit var useCase: GetLeadsUseCase

    @Before
    fun setUp() {
        repository = mockk()
        useCase = GetLeadsUseCase(repository)
    }

    @Test
    fun `invoke should call repository getLeads and return its flow`() = runTest {
        // Arrange
        val response = mockk<PagedBaseResponse<LeadResponse>>()
        val expectedFlow = flowOf(ResultState.Loading, ResultState.Success(response))
        
        every { repository.getLeads(any(), any(), any(), any(), any()) } returns expectedFlow

        // Act & Assert
        useCase().test {
            assertEquals(ResultState.Loading, awaitItem())
            val success = awaitItem() as ResultState.Success
            assertEquals(response, success.data)
            awaitComplete()
        }
    }
}
