package com.nusatim.partner.core.network

import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.ProjectResponse
import retrofit2.http.*

interface ProjectsApiService {

    @GET("projects")
    suspend fun getProjects(
        @Query("page") page: Int? = null,
        @Query("per_page") perPage: Int? = null
    ): PagedBaseResponse<ProjectResponse>

    @GET("projects/{id}")
    suspend fun getProjectDetail(@Path("id") id: Int): BaseResponse<ProjectResponse>

    @POST("projects/{id}/claim")
    suspend fun claimProject(@Path("id") id: Int): BaseResponse<ProjectResponse>

    @POST("projects/{id}/cancel-claim")
    suspend fun cancelClaimProject(@Path("id") id: Int): BaseResponse<ProjectResponse>
}
