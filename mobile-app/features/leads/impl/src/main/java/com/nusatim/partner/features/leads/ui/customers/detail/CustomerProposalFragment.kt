package com.nusatim.partner.features.leads.ui.customers.detail

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.model.dto.LeadDocumentResponse
import com.nusatim.partner.features.leads.databinding.FragmentLeadDocumentBinding
import com.nusatim.partner.features.leads.ui.leads.detail.LeadDocumentAdapter
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class CustomerProposalFragment : BaseFragment<FragmentLeadDocumentBinding>() {

    private val viewModel: CustomerDetailViewModel by viewModels({ requireParentFragment() })
    private val adapter by lazy {
        LeadDocumentAdapter { _ ->
            // TODO: View/Download
        }
    }

    override fun onInitViews() {
        binding.rvDocuments.layoutManager = LinearLayoutManager(requireContext())
        binding.rvDocuments.adapter = adapter
        binding.fabUpload.visibility = android.view.View.GONE // Read-only
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    val customer = state.customer
                    if (customer != null) {
                        val documents = customer.proposalDocuments?.map {
                            LeadDocumentResponse(
                                id = it.id,
                                leadId = customer.id,
                                originalName = it.name.orEmpty(),
                                downloadUrl = it.url,
                                createdAt = ""
                            )
                        } ?: emptyList()
                        
                        adapter.submitList(documents)
                        
                        val isEmpty = documents.isEmpty()
                        binding.layoutEmpty.root.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                        if (isEmpty) {
                            binding.layoutEmpty.tvErrorMessage.text = "Belum ada dokumen proposal"
                            binding.layoutEmpty.btnRetry.visibility = android.view.View.GONE
                        }
                    } else {
                        binding.layoutEmpty.root.visibility = android.view.View.VISIBLE
                        binding.layoutEmpty.tvErrorMessage.text = "Data customer tidak ditemukan"
                        binding.layoutEmpty.btnRetry.visibility = android.view.View.GONE
                    }
                }
            }
        }
    }
}
