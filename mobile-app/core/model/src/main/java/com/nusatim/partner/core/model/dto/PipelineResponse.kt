package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class PipelineResponse(
    @SerializedName("new") val newLeads: List<LeadResponse>,
    @SerializedName("contacted") val contactedLeads: List<LeadResponse>,
    @SerializedName("qualified") val qualifiedLeads: List<LeadResponse>,
    @SerializedName("opportunity") val opportunityLeads: List<LeadResponse>,
    @SerializedName("proposal") val proposalLeads: List<LeadResponse>,
    @SerializedName("negotiation") val negotiationLeads: List<LeadResponse>,
    @SerializedName("won") val wonLeads: List<LeadResponse>,
    @SerializedName("lost") val lostLeads: List<LeadResponse>
)
