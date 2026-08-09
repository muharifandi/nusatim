package com.nusatim.partner.features.leads.ui.leads.form

import android.widget.ArrayAdapter
import androidx.core.os.bundleOf
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.databinding.FragmentLeadFormBinding
import com.nusatim.partner.features.leads.ui.leads.detail.LeadDetailViewModel
import com.nusatim.partner.features.leads.ui.leads.detail.state.LeadDetailIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class LeadFormFragment : BaseFragment<FragmentLeadFormBinding>() {

    private val viewModel: LeadDetailViewModel by viewModels()
    private val leadId by lazy { arguments?.getInt("id") ?: -1 }

    override fun onInitViews() {
        binding.toolbar.setNavigationOnClickListener {
            findNavController().navigateUp()
        }

        if (leadId != -1) {
            binding.toolbar.title = getString(com.nusatim.partner.features.leads.R.string.lead_form_edit_title)
            viewModel.processIntent(LeadDetailIntent.LoadLead(leadId))
        }

        setupDropdown()

        binding.btnSave.setOnClickListener {
            val name = binding.etName.text.toString()
            val phone = binding.etPhone.text.toString()
            val email = binding.etEmail.text.toString()
            val estimation = binding.etEstimation.text.toString().toLongOrNull()
            
            // TODO: Get service_id from dropdown selection
            val request = mutableMapOf<String, Any?>(
                "name" to name,
                "phone" to phone,
                "email" to if (email.isNotEmpty()) email else null,
                "estimated_value" to estimation
            )
            
            if (leadId != -1) {
                viewModel.processIntent(LeadDetailIntent.UpdateLead(leadId, request))
            } else {
                viewModel.processIntent(LeadDetailIntent.CreateLead(request))
            }
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        state.lead?.let { lead ->
                            if (binding.etName.text.isNullOrEmpty()) {
                                binding.etName.setText(lead.name)
                                binding.etPhone.setText(lead.phone)
                                binding.etEmail.setText(lead.email)
                                binding.etEstimation.setText(lead.estimatedValue?.toString())
                            }
                        }
                        binding.btnSave.isEnabled = !state.isLoading
                    }
                }

                launch {
                    viewModel.effect.collect { effect ->
                        // Handle success effect to navigate back
                    }
                }
            }
        }
    }

    private fun setupDropdown() {
        // Hardcoded products for now as discussed
        val products = arrayOf("Pemasangan Baru", "Maintenance", "Audit Energi")
        val adapter = ArrayAdapter(requireContext(), android.R.layout.simple_dropdown_item_1line, products)
        binding.acProduct.setAdapter(adapter)
    }
}
